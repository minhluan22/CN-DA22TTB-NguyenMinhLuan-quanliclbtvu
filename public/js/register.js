const passwordInput = document.getElementById('password');
const strengthBar = document.getElementById('strength-bar');

if(passwordInput){
    passwordInput.addEventListener('input', () => {
        const value = passwordInput.value;
        let strength = 0;

        if (value.length >= 6) strength += 30;
        if (/[A-Z]/.test(value)) strength += 30;
        if (/[0-9]/.test(value)) strength += 20;
        if (/[^A-Za-z0-9]/.test(value)) strength += 20;

        strengthBar.style.width = strength + '%';

        if (strength < 40) strengthBar.style.background = 'red';
        else if (strength < 70) strengthBar.style.background = '#FFE600';
        else strengthBar.style.background = '#0B3D91';
    });
}
