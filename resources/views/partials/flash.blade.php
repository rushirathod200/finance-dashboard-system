@if (session('status') || $errors->any())
    <div class="flash-stack" data-auto-dismiss="3000">
        @if (session('status'))
            <div class="flash-card success">
                <div class="flash-title">Success</div>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="flash-card error">
                <div class="flash-title">Please review the following</div>

                <ul class="flash-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
