@php
    $activityTypeIcons = [
        'academic' => '📚',
        'arts' => '🎭',
        'volunteer' => '🤝',
        'other' => '📋'
    ];
    $activityTypeColors = [
        'academic' => '#1976d2',
        'arts' => '#c2185b',
        'volunteer' => '#388e3c',
        'other' => '#f57c00'
    ];
    $icon = $activityTypeIcons[$event->activity_type ?? 'other'] ?? '📋';
    $iconColor = $activityTypeColors[$event->activity_type ?? 'other'] ?? '#f57c00';
@endphp

<div class="event-card" id="event-{{ $event->id }}">
    {{-- Icon bên trái --}}
    <div class="event-card-icon" style="background: {{ $iconColor }}20; color: {{ $iconColor }}; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; flex-shrink: 0;">
        {{ $icon }}
    </div>

    {{-- Thông tin ở giữa --}}
    <div class="event-card-info" style="flex: 1; margin-left: 16px; min-width: 0;">
        <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700; color: var(--text-dark);">
            {{ $event->title }}
        </h4>
        <div class="event-meta" style="font-size: 13px; color: var(--muted); margin-bottom: 6px;">
            <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') }}</span>
            <span style="margin: 0 8px;">|</span>
            <span><i class="bi bi-geo-alt"></i> {{ $event->location ?? 'Chưa cập nhật' }}</span>
        </div>
        @if($event->description)
            <p style="font-size: 13px; color: var(--muted); margin: 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                {{ Str::limit($event->description, 80) }}
            </p>
        @endif
    </div>

    {{-- Badge và CTA bên phải --}}
    <div class="event-card-actions" style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0; margin-left: 16px;">
        @if($status == 'ongoing')
            <span class="badge" style="background: #0dcaf0; color: #000; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                Đang diễn ra
            </span>
            <a href="{{ route('student.activity-detail', $event->id) }}" class="btn btn-sm btn-primary" style="font-size: 12px; padding: 6px 12px;">
                <i class="bi bi-eye"></i> Xem chi tiết
            </a>
        @elseif($status == 'upcoming')
            @if($event->approval_status === 'rejected')
                <span class="badge" style="background: #f8d7da; color: #721c24; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                    Bị từ chối
                </span>
            @elseif(isset($event->registration))
                @php $registered = $event->registration; @endphp
                @if($registered->status === 'pending')
                    <span class="badge" style="background: #fff3cd; color: #856404; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                        Chờ duyệt
                    </span>
                    <form action="{{ route('student.cancel-event-registration', $registered->id) }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-secondary" style="font-size: 12px; padding: 6px 12px;">
                            <i class="bi bi-x-circle"></i> Hủy đăng ký
                        </button>
                    </form>
                @elseif($registered->status === 'approved')
                    <span class="badge" style="background: #d4edda; color: #155724; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                        Đã đăng ký
                    </span>
                    <a href="{{ route('student.activity-detail', $event->id) }}" class="btn btn-sm btn-primary" style="font-size: 12px; padding: 6px 12px;">
                        <i class="bi bi-eye"></i> Xem chi tiết
                    </a>
                @elseif($registered->status === 'rejected')
                    <span class="badge" style="background: #f8d7da; color: #721c24; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                        Bị từ chối
                    </span>
                    @if($registered->notes)
                        <small style="font-size: 11px; color: var(--muted); text-align: right; display: block; max-width: 150px;">
                            Lý do: {{ Str::limit($registered->notes, 30) }}
                        </small>
                    @endif
                @endif
            @else
                <span class="badge" style="background: #cfe2ff; color: #084298; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                    Sắp diễn ra
                </span>
                <form action="{{ route('student.register-event', $event->id) }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary" style="font-size: 12px; padding: 6px 12px;">
                        <i class="bi bi-plus-circle"></i> Đăng ký
                    </button>
                </form>
            @endif
        @elseif($status == 'finished')
            <span class="badge" style="background: #d4edda; color: #155724; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                Đã kết thúc
            </span>
            @if(isset($event->activity_points))
                <span style="font-size: 12px; color: var(--muted);">
                    ⭐ {{ $event->activity_points }} điểm
                </span>
            @endif
            <a href="{{ route('student.activity-detail', $event->id) }}" class="btn btn-sm btn-primary" style="font-size: 12px; padding: 6px 12px;">
                <i class="bi bi-eye"></i> Xem chi tiết
            </a>
        @endif
    </div>
</div>

