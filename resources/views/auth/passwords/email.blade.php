<x-auth-master>
    @section('content')
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="mt-4 card card-bg-fill">
                    <div class="p-4 card-body">
                        <div class="mt-2 text-center">
                            <h5 class="text-primary">{{ app_name() }}</h5>
                            <p class="text-muted">Reset Password</p>
                        </div>
                        <div class="p-2 mt-4">
                            <div>
                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror "
                                        id="email" name="email" value="{{ old('email') }}" required
                                        autocomplete="email" autofocus placeholder="Enter email">
                                    @error('email')
                                        <p class="mt-1 text-danger">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mt-4">
                                    <button class="btn btn-success w-100" type="submit">Submit</button>
                                </div>
                                <div class="mt-2">
                                    <div class="float-end">
                                        <a href="{{ route('login') }}" class="text-muted">Back to login</a>
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
