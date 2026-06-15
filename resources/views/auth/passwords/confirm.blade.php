
<x-auth-master>

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="mt-4 card card-bg-fill">
            <div class="p-4 card-body">
                <div class="mt-2 text-center">
                    <h5 class="text-primary">{{ app_name() }}</h5>
                    <p class="text-muted">{{ __('Confirm Password') }}</p>
                </div>
                <div class="p-2 mt-4">
                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror "
                                id="password" name="password" value="{{ old('password') }}" required
                                autocomplete="current-password" autofocus placeholder="Enter password">
                            @error('password')
                                <p class="mt-1 text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-success w-100" type="submit">  {{ __('Confirm Password') }}</button>
                        </div>
                        <div class="mt-2">
                            <div class="float-end">
                                <a href="{{ route('password.request') }}" class="text-muted"> {{ __('Forgot Your Password?') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>
</div>
@endsection
</x-auth-master>
