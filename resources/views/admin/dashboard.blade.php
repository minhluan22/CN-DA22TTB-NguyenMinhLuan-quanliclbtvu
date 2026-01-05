@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
    }
    
    .dashboard-container {
        /* Removed - using container-fluid mt-3 instead */
    }
    
    /* KPI Cards với gradient và animations */
    .kpi-card {
        background: white;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 8px 24px rgba(0,0,0,0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.8);
    }
    
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, transparent, currentColor, transparent);
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .kpi-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 12px 32px rgba(0,0,0,0.12), 0 4px 16px rgba(0,0,0,0.08);
    }
    
    .kpi-card:hover::before {
        opacity: 1;
    }
    
    .kpi-card.primary { 
        border-left: 4px solid #0033A0;
        color: #0033A0;
    }
    .kpi-card.primary::before {
        background: linear-gradient(90deg, transparent, #0033A0, transparent);
    }
    
    .kpi-card.success { 
        border-left: 4px solid #5FB84A;
        color: #5FB84A;
    }
    .kpi-card.success::before {
        background: linear-gradient(90deg, transparent, #5FB84A, transparent);
    }
    
    .kpi-card.warning { 
        border-left: 4px solid #FFE600;
        color: #FFA500;
    }
    .kpi-card.warning::before {
        background: linear-gradient(90deg, transparent, #FFE600, transparent);
    }
    
    .kpi-card.danger { 
        border-left: 4px solid #0B3D91;
        color: #0B3D91;
    }
    .kpi-card.danger::before {
        background: linear-gradient(90deg, transparent, #0B3D91, transparent);
    }
    
    .kpi-card.info { 
        border-left: 4px solid #0B3D91;
        color: #0B3D91;
    }
    .kpi-card.info::before {
        background: linear-gradient(90deg, transparent, #0B3D91, transparent);
    }
    
    .kpi-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 20px;
        position: relative;
        transition: all 0.3s;
    }
    
    .kpi-card:hover .kpi-icon {
        transform: scale(1.1) rotate(5deg);
    }
    
    .kpi-card.primary .kpi-icon { 
        background: linear-gradient(135deg, rgba(0, 51, 160, 0.15) 0%, rgba(0, 51, 160, 0.05) 100%);
    }
    .kpi-card.success .kpi-icon { 
        background: linear-gradient(135deg, rgba(95, 184, 74, 0.15) 0%, rgba(95, 184, 74, 0.05) 100%);
    }
    .kpi-card.warning .kpi-icon { 
        background: linear-gradient(135deg, rgba(255, 230, 0, 0.15) 0%, rgba(255, 230, 0, 0.05) 100%);
    }
    .kpi-card.danger .kpi-icon { 
        background: linear-gradient(135deg, rgba(11, 61, 145, 0.15) 0%, rgba(11, 61, 145, 0.05) 100%);
    }
    .kpi-card.info .kpi-icon { 
        background: linear-gradient(135deg, rgba(11, 61, 145, 0.15) 0%, rgba(11, 61, 145, 0.05) 100%);
    }
    
    .kpi-value {
        font-size: 42px;
        font-weight: 800;
        margin: 12px 0;
        line-height: 1.1;
        background: linear-gradient(135deg, currentColor 0%, currentColor 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .kpi-label {
        font-size: 15px;
        color: #6b7280;
        font-weight: 600;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .kpi-change {
        font-size: 13px;
        font-weight: 700;
        margin-top: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        width: fit-content;
    }
    .kpi-change.positive { 
        color: #5FB84A; 
        background: rgba(95, 184, 74, 0.1);
    }
    .kpi-change.negative { 
        color: #0B3D91; 
        background: rgba(11, 61, 145, 0.1);
    }
    .kpi-change.neutral { 
        color: #6b7280; 
        background: rgba(107, 114, 128, 0.1);
    }
    
    .kpi-status {
        font-size: 11px;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 700;
        margin-top: 12px;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .kpi-status.good { 
        background: linear-gradient(135deg, #8EDC6E 0%, #5FB84A 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(95, 184, 74, 0.3);
    }
    .kpi-status.warning { 
        background: linear-gradient(135deg, #fff9e6 0%, #FFE600 100%);
        color: #1f1f1f;
    }
    .kpi-status.danger { 
        background: linear-gradient(135deg, #E6F0FF 0%, #0B3D91 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(11, 61, 145, 0.3);
    }
    
    /* Risk Level Summary */
    .risk-summary {
        background: white;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 8px 24px rgba(0,0,0,0.04);
        border: 1px solid rgba(255,255,255,0.8);
    }
    
    .risk-summary h3 {
        font-size: 20px;
        font-weight: 800;
        color: #1f1f1f;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .risk-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 20px;
        border-radius: 16px;
        margin-bottom: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .risk-item-content {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
    }
    
    .risk-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }
    
    .risk-item:hover {
        transform: translateX(8px) scale(1.02);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    
    .risk-item:hover::before {
        left: 100%;
    }
    
    .risk-item.risk-good { 
        background: linear-gradient(135deg, #8EDC6E 0%, #5FB84A 100%);
        box-shadow: 0 4px 16px rgba(95, 184, 74, 0.3);
    }
    .risk-item.risk-warning { 
        background: linear-gradient(135deg, #fff9e6 0%, #FFE600 100%);
        box-shadow: 0 4px 16px rgba(255, 230, 0, 0.3);
    }
    .risk-item.risk-danger { 
        background: linear-gradient(135deg, #E6F0FF 0%, #0B3D91 100%);
        box-shadow: 0 4px 16px rgba(11, 61, 145, 0.3);
    }
    
    .risk-icon {
        font-size: 40px;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    .risk-info {
        flex: 1;
    }
    
    .risk-value {
        font-size: 36px;
        font-weight: 800;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .risk-label {
        font-size: 14px;
        font-weight: 600;
        margin-top: 6px;
        opacity: 0.9;
    }
    
    .risk-good .risk-value { color: white; }
    .risk-good .risk-label { color: rgba(255,255,255,0.9); }
    
    .risk-warning .risk-value { color: #1f1f1f; }
    .risk-warning .risk-label { color: rgba(31,31,31,0.8); }
    
    .risk-danger .risk-value { color: white; }
    .risk-danger .risk-label { color: rgba(255,255,255,0.9); }
    
    /* Violation Card - Modern Design */
    .violation-card {
        background: linear-gradient(135deg, #ffffff 0%, #E6F0FF 100%);
        border-radius: 20px;
        padding: 32px;
        box-shadow: 0 4px 16px rgba(11, 61, 145, 0.15), 0 8px 32px rgba(11, 61, 145, 0.1);
        border: 2px solid rgba(11, 61, 145, 0.1);
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .violation-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(11, 61, 145, 0.05) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }
    
    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .violation-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 12px 40px rgba(11, 61, 145, 0.25), 0 8px 24px rgba(11, 61, 145, 0.15);
        border-color: rgba(11, 61, 145, 0.3);
    }
    
    .violation-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        position: relative;
        z-index: 1;
    }
    
    .violation-icon-wrapper {
        position: relative;
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .violation-icon {
        font-size: 48px;
        filter: drop-shadow(0 4px 8px rgba(11, 61, 145, 0.3));
        animation: bounce 2s ease-in-out infinite;
        position: relative;
        z-index: 2;
    }
    
    @keyframes bounce {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-8px) scale(1.1); }
    }
    
    .violation-pulse {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(11, 61, 145, 0.2);
        animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse-ring {
        0% {
            transform: translate(-50%, -50%) scale(0.8);
            opacity: 1;
        }
        100% {
            transform: translate(-50%, -50%) scale(1.5);
            opacity: 0;
        }
    }
    
    .violation-badge {
        background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%);
        color: white;
        font-size: 42px;
        font-weight: 800;
        padding: 12px 24px;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(11, 61, 145, 0.4), inset 0 2px 4px rgba(255, 255, 255, 0.2);
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        position: relative;
        z-index: 1;
        animation: number-glow 2s ease-in-out infinite;
    }
    
    @keyframes number-glow {
        0%, 100% {
            box-shadow: 0 4px 16px rgba(11, 61, 145, 0.4), inset 0 2px 4px rgba(255, 255, 255, 0.2);
        }
        50% {
            box-shadow: 0 6px 24px rgba(11, 61, 145, 0.6), inset 0 2px 4px rgba(255, 255, 255, 0.2);
        }
    }
    
    .violation-content {
        flex: 1;
        margin-bottom: 24px;
        position: relative;
        z-index: 1;
    }
    
    .violation-title {
        font-size: 20px;
        font-weight: 800;
        color: #1f1f1f;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }
    
    .violation-subtitle {
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
        line-height: 1.5;
    }
    
    .violation-btn {
        background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%);
        color: white;
        border: none;
        padding: 16px 24px;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 16px rgba(11, 61, 145, 0.4);
        position: relative;
        z-index: 1;
        overflow: hidden;
    }
    
    .violation-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .violation-btn:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .violation-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(11, 61, 145, 0.5);
        color: white;
        text-decoration: none;
    }
    
    .violation-btn span {
        position: relative;
        z-index: 1;
    }
    
    .violation-btn svg {
        position: relative;
        z-index: 1;
        transition: transform 0.3s;
    }
    
    .violation-btn:hover svg {
        transform: translateX(4px);
    }
    
    .violation-decoration {
        position: absolute;
        bottom: -20px;
        right: -20px;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(11, 61, 145, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 0;
    }
    
    .risk-view-btn {
        background: rgba(255, 255, 255, 0.9);
        color: #1f1f1f;
        border: none;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        position: relative;
        z-index: 1;
        white-space: nowrap;
    }
    
    .risk-view-btn:hover {
        background: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        color: #1f1f1f;
        text-decoration: none;
    }
    
    .risk-item.risk-good .risk-view-btn {
        color: #5FB84A;
    }
    
    .risk-item.risk-warning .risk-view-btn {
        color: #FFA500;
    }
    
    .risk-item.risk-danger .risk-view-btn {
        color: #0B3D91;
    }
    
    /* Action Required */
    .action-card {
        background: white;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 8px 24px rgba(0,0,0,0.04);
        margin-bottom: 24px;
        border: 1px solid rgba(255,255,255,0.8);
        transition: all 0.3s;
    }
    
    .action-card:hover {
        box-shadow: 0 12px 32px rgba(0,0,0,0.12), 0 4px 16px rgba(0,0,0,0.08);
        transform: translateY(-4px);
    }
    
    .action-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 3px solid #f3f4f6;
        position: relative;
    }
    
    .action-header::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 0;
        width: 60px;
        height: 3px;
        border-radius: 3px;
        transition: width 0.3s;
    }
    
    .action-card:hover .action-header::after {
        width: 100%;
    }
    
    .action-header.danger { 
        border-bottom-color: #E6F0FF;
    }
    .action-header.danger::after {
        background: linear-gradient(90deg, #0B3D91, #0033A0);
    }
    
    .action-header.warning { 
        border-bottom-color: #fff9e6;
    }
    .action-header.warning::after {
        background: linear-gradient(90deg, #FFE600, #ffd700);
    }
    
    .action-header.info { 
        border-bottom-color: #E6F0FF;
    }
    .action-header.info::after {
        background: linear-gradient(90deg, #0B3D91, #0033A0);
    }
    
    .action-icon {
        font-size: 28px;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
    }
    
    .action-title {
        font-size: 20px;
        font-weight: 800;
        color: #1f1f1f;
        margin: 0;
    }
    
    .action-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .action-item {
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s;
        cursor: pointer;
        border: 1px solid transparent;
    }
    
    .action-item:hover {
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        border-color: rgba(11, 61, 145, 0.2);
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    .action-item-name {
        font-weight: 700;
        color: #1f1f1f;
        margin-bottom: 6px;
        font-size: 15px;
    }
    
    .action-item-reason {
        font-size: 13px;
        color: #6b7280;
        font-weight: 500;
    }
    
    .action-btn {
        background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(11, 61, 145, 0.3);
    }
    
    .action-btn:hover {
        background: linear-gradient(135deg, #0033A0 0%, #0B3D91 100%);
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(11, 61, 145, 0.4);
    }
    
    /* Chart Cards */
    .chart-card {
        background: white;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 8px 24px rgba(0,0,0,0.04);
        height: 100%;
        border: 1px solid rgba(255,255,255,0.8);
        transition: all 0.3s;
    }
    
    .chart-card:hover {
        box-shadow: 0 12px 32px rgba(0,0,0,0.12), 0 4px 16px rgba(0,0,0,0.08);
        transform: translateY(-4px);
    }
    
    .chart-title {
        font-size: 20px;
        font-weight: 800;
        color: #1f1f1f;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    /* Quick Actions */
    .quick-actions {
        background: white;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 8px 24px rgba(0,0,0,0.04);
        border: 1px solid rgba(255,255,255,0.8);
    }
    
    .quick-actions h3 {
        font-size: 20px;
        font-weight: 800;
        color: #1f1f1f;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .quick-action-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    
    .quick-action-btn {
        background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%);
        color: white;
        border: none;
        padding: 24px;
        border-radius: 16px;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(11, 61, 145, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .quick-action-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .quick-action-btn:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .quick-action-btn:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 12px 24px rgba(11, 61, 145, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .quick-action-icon {
        font-size: 28px;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        position: relative;
        z-index: 1;
    }
    
    .quick-action-btn span:not(.quick-action-icon) {
        position: relative;
        z-index: 1;
    }
    
    /* Top Reports */
    .report-card {
        background: white;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 8px 24px rgba(0,0,0,0.04);
        height: 100%;
        border: 1px solid rgba(255,255,255,0.8);
        transition: all 0.3s;
    }
    
    .report-card:hover {
        box-shadow: 0 12px 32px rgba(0,0,0,0.12), 0 4px 16px rgba(0,0,0,0.08);
        transform: translateY(-4px);
    }
    
    .report-item {
        padding: 16px;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s;
        border-radius: 8px;
        margin-bottom: 8px;
    }
    
    .report-item:hover {
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        transform: translateX(4px);
    }
    
    .report-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .report-name {
        font-weight: 700;
        color: #1f1f1f;
        margin-bottom: 6px;
        font-size: 15px;
    }
    
    .report-meta {
        font-size: 13px;
        color: #6b7280;
        font-weight: 500;
    }
    
    .report-value {
        font-size: 28px;
        font-weight: 800;
        background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>
@endpush

@section('content')
@if(isset($error))
    <div class="alert alert-danger alert-dismissible fade show m-3">
        <strong>❌ Lỗi:</strong> {{ $error }}
        <p class="mb-0"><small>Vui lòng kiểm tra log hoặc liên hệ quản trị viên.</small></p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="container-fluid mt-3">
    <div style="margin-bottom: 32px;">
        <h1 style="margin: 0; color: #1f1f1f; font-size: 32px; font-weight: 800; display: flex; align-items: center; gap: 12px;">
            <span style="font-size: 36px;">📊</span>
            <span>Dashboard Admin</span>
        </h1>
        <p style="margin: 8px 0 0 0; color: #6b7280; font-size: 15px; font-weight: 500;">
            Tổng quan hệ thống và thống kê hoạt động
        </p>
    </div>
    {{-- HÀNG 1: KPI TỔNG QUAN --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px;">
        {{-- Tổng CLB --}}
        <div class="kpi-card primary">
            <div class="kpi-icon" style="background: rgba(0, 51, 160, 0.1); color: #0033A0;">🏢</div>
            <div class="kpi-value">{{ $totalClubs ?? 0 }}</div>
            <div class="kpi-label">Tổng số CLB</div>
            <div class="kpi-status good">Hệ thống ổn định</div>
        </div>
        
        {{-- CLB đang hoạt động --}}
        <div class="kpi-card success">
            <div class="kpi-icon" style="background: rgba(95, 184, 74, 0.1); color: #5FB84A;">✅</div>
            <div class="kpi-value" style="color: #5FB84A;">{{ $activeClubs ?? 0 }}</div>
            <div class="kpi-label">CLB đang hoạt động</div>
            @if(isset($activeClubsChange))
                <div class="kpi-change {{ $activeClubsChange > 0 ? 'positive' : ($activeClubsChange < 0 ? 'negative' : 'neutral') }}">
                    @if($activeClubsChange > 0)▲ @elseif($activeClubsChange < 0)▼ @else ➡️ @endif
                    {{ abs($activeClubsChange) }}
                </div>
            @endif
        </div>
        
        {{-- Tổng thành viên --}}
        <div class="kpi-card info">
            <div class="kpi-icon" style="background: rgba(11, 61, 145, 0.1); color: #0B3D91;">👥</div>
            <div class="kpi-value" style="color: #0B3D91;">{{ number_format($totalMembers ?? 0) }}</div>
            <div class="kpi-label">Tổng thành viên CLB</div>
            @if(isset($totalMembersChange))
                <div class="kpi-change {{ $totalMembersChange > 0 ? 'positive' : ($totalMembersChange < 0 ? 'negative' : 'neutral') }}">
                    @if($totalMembersChange > 0)▲ @elseif($totalMembersChange < 0)▼ @else ➡️ @endif
                    {{ abs($totalMembersChange) }}
                </div>
            @endif
        </div>
        
        {{-- Hoạt động đã tổ chức --}}
        <div class="kpi-card warning">
            <div class="kpi-icon" style="background: rgba(255, 230, 0, 0.1); color: #FFE600;">📅</div>
            <div class="kpi-value" style="color: #FFE600;">{{ $finishedEvents ?? 0 }}</div>
            <div class="kpi-label">Hoạt động đã tổ chức</div>
            @if(isset($finishedEventsChange))
                <div class="kpi-change {{ $finishedEventsChange > 0 ? 'positive' : ($finishedEventsChange < 0 ? 'negative' : 'neutral') }}">
                    @if($finishedEventsChange > 0)▲ @elseif($finishedEventsChange < 0)▼ @else ➡️ @endif
                    {{ abs($finishedEventsChange) }}
                </div>
            @endif
        </div>
    </div>
    
    {{-- HÀNG 2: Risk Level + Vi phạm đang xử lý --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
        {{-- Risk Level Summary --}}
        <div class="risk-summary">
            <h3 style="margin: 0 0 20px 0; font-size: 18px; font-weight: 700; color: #1f1f1f;">📊 Phân tầng rủi ro CLB</h3>
            @if(isset($riskSummary))
                <div class="risk-item risk-good">
                    <div class="risk-item-content">
                        <div class="risk-icon">🟢</div>
                        <div class="risk-info">
                            <div class="risk-value" style="color: white;">{{ $riskSummary['good'] ?? 0 }}</div>
                            <div class="risk-label">CLB hoạt động tốt</div>
                        </div>
                    </div>
                    @if(($riskSummary['good'] ?? 0) > 0)
                        <a href="{{ route('admin.clubs.index', ['risk_level' => 'good']) }}" class="risk-view-btn">
                            Xem chi tiết →
                        </a>
                    @endif
                </div>
                <div class="risk-item risk-warning">
                    <div class="risk-item-content">
                        <div class="risk-icon">🟡</div>
                        <div class="risk-info">
                            <div class="risk-value" style="color: #1f1f1f;">{{ $riskSummary['warning'] ?? 0 }}</div>
                            <div class="risk-label">CLB cần cảnh báo</div>
                        </div>
                    </div>
                    @if(($riskSummary['warning'] ?? 0) > 0)
                        <a href="{{ route('admin.clubs.index', ['risk_level' => 'warning']) }}" class="risk-view-btn">
                            Xem chi tiết →
                        </a>
                    @endif
                </div>
                <div class="risk-item risk-danger">
                    <div class="risk-item-content">
                        <div class="risk-icon">🔵</div>
                        <div class="risk-info">
                            <div class="risk-value" style="color: white;">{{ $riskSummary['danger'] ?? 0 }}</div>
                            <div class="risk-label">CLB có nguy cơ</div>
                        </div>
                    </div>
                    @if(($riskSummary['danger'] ?? 0) > 0)
                        <a href="{{ route('admin.clubs.index', ['risk_level' => 'danger']) }}" class="risk-view-btn">
                            Xem chi tiết →
                        </a>
                    @endif
                </div>
            @endif
        </div>
        
        {{-- Vi phạm đang xử lý --}}
        <div class="violation-card">
            <div class="violation-card-header">
                <div class="violation-icon-wrapper">
                    <div class="violation-icon">⚠️</div>
                    <div class="violation-pulse"></div>
                </div>
                <div class="violation-badge">{{ $pendingViolations ?? 0 }}</div>
            </div>
            <div class="violation-content">
                <div class="violation-title">Vi phạm đang xử lý</div>
                <div class="violation-subtitle">Cần xem xét và xử lý ngay</div>
            </div>
            <a href="{{ route('admin.activities.violations') }}" class="violation-btn">
                <span>Xem chi tiết</span>
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <div class="violation-decoration"></div>
        </div>
    </div>
    
    {{-- HÀNG 3: CẦN XỬ LÝ NGAY (Action Required) --}}
    @if(isset($actionRequired) && count($actionRequired) > 0)
        <div style="margin-bottom: 32px;">
            <h2 style="font-size: 24px; font-weight: 800; color: #1f1f1f; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                <span>🚨</span>
                <span>Cần xử lý ngay</span>
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">
                @foreach($actionRequired as $action)
                    <div class="action-card">
                        <div class="action-header {{ $action['type'] }}">
                            <div class="action-icon">{{ $action['icon'] }}</div>
                            <h3 class="action-title">{{ $action['title'] }}</h3>
                        </div>
                        <ul class="action-list">
                            @foreach($action['items'] as $item)
                                <li class="action-item" onclick="window.location.href='{{ $item['url'] ?? '#' }}'">
                                    <div>
                                        <div class="action-item-name">{{ $item['name'] ?? $item['title'] }}</div>
                                        <div class="action-item-reason">{{ $item['reason'] ?? $item['club'] ?? '' }}</div>
                                    </div>
                                    <a href="{{ $item['url'] ?? '#' }}" class="action-btn">Xem</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    {{-- HÀNG 4: BIỂU ĐỒ QUẢN LÝ --}}
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;">
        {{-- Biểu đồ xu hướng hoạt động --}}
        <div class="chart-card">
            <div class="chart-title">📈 Xu hướng hoạt động (6 tháng gần nhất)</div>
            <div style="height: 300px;">
                <canvas id="eventTrendChart"></canvas>
            </div>
        </div>
        
        {{-- Biểu đồ phân loại lĩnh vực --}}
        <div class="chart-card">
            <div class="chart-title">🎯 Phân loại lĩnh vực</div>
            <div style="height: 300px;">
                <canvas id="clubFieldChart"></canvas>
            </div>
        </div>
    </div>
    
    {{-- HÀNG 5: BIỂU ĐỒ VI PHẠM + TOP BÁO CÁO --}}
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;">
        {{-- Biểu đồ vi phạm theo loại --}}
        @if(isset($violationByType) && $violationByType->count() > 0)
            <div class="chart-card">
                <div class="chart-title">⚠️ Vi phạm theo loại</div>
                <div style="height: 300px;">
                    <canvas id="violationTypeChart"></canvas>
                </div>
            </div>
        @endif
        
        {{-- Top 5 CLB hoạt động tốt nhất --}}
        <div class="report-card">
            <div class="chart-title">🏆 Top 5 CLB hoạt động tốt nhất</div>
            <div style="margin-top: 16px;">
                @if(isset($topActiveClubs) && $topActiveClubs->count() > 0)
                    @foreach($topActiveClubs as $index => $club)
                        <div class="report-item">
                            <div>
                                <div class="report-name">{{ $club->name }}</div>
                                <div class="report-meta">{{ $club->event_count }} hoạt động • {{ $club->violation_count }} vi phạm</div>
                            </div>
                            <div class="report-value">#{{ $index + 1 }}</div>
                        </div>
                    @endforeach
                @else
                    <p style="text-align: center; color: #6b7280; padding: 20px;">Chưa có dữ liệu</p>
                @endif
            </div>
        </div>
    </div>
    
    {{-- HÀNG 6: QUICK ACTIONS --}}
    <div class="quick-actions">
        <h3 style="margin: 0 0 16px 0; font-size: 18px; font-weight: 700; color: #1f1f1f;">⚡ Thao tác nhanh</h3>
        <div class="quick-action-grid">
            <a href="{{ route('admin.clubs.index') }}" class="quick-action-btn">
                <span class="quick-action-icon">➕</span>
                <span>Tạo CLB mới</span>
            </a>
            <a href="{{ route('admin.clubs.index') }}" class="quick-action-btn">
                <span class="quick-action-icon">📋</span>
                <span>Danh sách CLB</span>
            </a>
            <a href="{{ route('admin.activities.index') }}" class="quick-action-btn">
                <span class="quick-action-icon">📅</span>
                <span>Quản lý hoạt động</span>
            </a>
            <a href="{{ route('admin.regulations.index') }}" class="quick-action-btn">
                <span class="quick-action-icon">📕</span>
                <span>Quản lý nội quy</span>
            </a>
            <a href="{{ route('admin.statistics.overview') }}" class="quick-action-btn">
                <span class="quick-action-icon">📊</span>
                <span>Thống kê - Báo cáo</span>
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Biểu đồ xu hướng hoạt động
const eventTrendCtx = document.getElementById('eventTrendChart');
if (eventTrendCtx) {
    const eventTrends = {!! json_encode($eventTrends ?? []) !!};
    new Chart(eventTrendCtx, {
        type: 'line',
        data: {
            labels: eventTrends.map(item => item.month),
            datasets: [{
                label: 'Số hoạt động',
                data: eventTrends.map(item => item.count),
                borderColor: '#0B3D91',
                backgroundColor: 'rgba(11, 61, 145, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#FFE600',
                pointBorderColor: '#0B3D91',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Biểu đồ phân loại lĩnh vực
const clubFieldCtx = document.getElementById('clubFieldChart');
if (clubFieldCtx) {
    const clubFields = {!! json_encode($clubFieldStats ?? []) !!};
    new Chart(clubFieldCtx, {
        type: 'doughnut',
        data: {
            labels: clubFields.map(item => item.field || 'Chưa xác định'),
            datasets: [{
                data: clubFields.map(item => item.count),
                backgroundColor: [
                    '#0033A0',
                    '#0B3D91',
                    '#FFE600',
                    '#5FB84A',
                    '#8EDC6E',
                    '#FFF3A0',
                    '#0B3D91',
                    '#9333ea',
                    '#10b981',
                    '#f59e0b'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 12,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed + ' CLB';
                            return label;
                        }
                    }
                }
            }
        }
    });
}

// Biểu đồ vi phạm theo loại
const violationTypeCtx = document.getElementById('violationTypeChart');
if (violationTypeCtx) {
    const violations = {!! json_encode($violationByType ?? []) !!};
    new Chart(violationTypeCtx, {
        type: 'bar',
        data: {
            labels: violations.map(item => {
                // Rút gọn tên vi phạm nếu quá dài
                const name = item.violation_type || 'Khác';
                return name.length > 30 ? name.substring(0, 30) + '...' : name;
            }),
            datasets: [{
                label: 'Số lượng vi phạm',
                data: violations.map(item => item.count),
                backgroundColor: '#0B3D91',
                borderColor: '#0033A0',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12
                }
            },
            scales: {
                x: { 
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}
</script>
@endpush
