{{-- resources/views/components/loader.blade.php --}}
<div id="loader-overlay" style="display: none;">
    <div id="loader"></div>
</div>

<style>
    #loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: none;
    }

    #loader {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        height: 60px;
        border: 6px solid #fff;
        border-top: 6px solid #621132;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: translate(-50%, -50%) rotate(0deg);
        }
        100% {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }
</style>

<script>
    window.loader = function (state) {
        const overlay = document.getElementById('loader-overlay');
        if (!overlay) return;

        switch (state) {
            case 'show':
                overlay.style.display = 'block';
                break;
            case 'hide':
                overlay.style.display = 'none';
                break;
            default:
                console.warn('loader() solo acepta "show" o "hide"');
        }
    }
</script>
