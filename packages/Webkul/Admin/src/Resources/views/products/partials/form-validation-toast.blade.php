@if ($errors->any())
    @pushOnce('scripts')
        <script>
            window.addEventListener('load', function () {
                var messages = @json(collect($errors->all())->unique()->values());

                setTimeout(function () {
                    messages.forEach(function (message) {
                        window.emitter && window.emitter.emit('add-flash', {
                            type: 'error',
                            message: message,
                        });
                    });
                }, 150);
            });
        </script>
    @endPushOnce
@endif
