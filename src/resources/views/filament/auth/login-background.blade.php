<style>
    .fi-simple-layout {
        background-color: #FBF1E4;
        background-image: url('{{ asset('images/auth/login-bg-light.svg') }}');
        background-repeat: no-repeat;
        background-position: center top;
        background-size: cover;
    }

    .dark .fi-simple-layout {
        background-color: #2B1B12;
        background-image: url('{{ asset('images/auth/login-bg-dark.svg') }}');
    }

    .fi-simple-main {
        backdrop-filter: blur(2px);
    }
</style>
