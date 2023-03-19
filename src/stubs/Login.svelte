<script>
    import route from 'ziggy-js';

    import GuestLayout from '../layouts/GuestLayout.svelte'

    // let loginRoute = route('login')

    export let errors

    // console.log(loginRoute)
</script>

<GuestLayout>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Login</div>

                    <div class="card-body">
                        <form method="POST" action={route('login')}>

                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">Email Address</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') @enderror" name="email" value="" required autocomplete="email">

                                    {#if errors.email}
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{errors.email}</strong>
                                        </span>
                                    {/if}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control @error('password') @enderror" name="password" required autocomplete="current-password">

                                    {#if errors.password}
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{errors.password}</strong>
                                        </span>
                                    {/if}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember">

                                        <label class="form-check-label" for="remember">
                                            Remember Me
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Login
                                    </button>

                                    {#if route('password.request')}
                                        <a class="btn btn-link" href={route('password.request')}>
                                            Forgot Your Password?
                                        </a>
                                    {/if}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</GuestLayout>
