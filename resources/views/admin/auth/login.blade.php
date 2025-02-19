@extends('admin.auth.cover')
@section('content')

	<div class="w-full max-w-sm px-4 py-6 space-y-6 bg-white rounded-md dark:bg-darker">
            <h1 class="text-xl font-semibold text-center">Login</h1>
            <form action="#" class="space-y-6">
              <input
                class="w-full px-4 py-2 border rounded-md dark:bg-darker dark:border-gray-700 focus:outline-none focus:ring focus:ring-primary-100 dark:focus:ring-primary-darker"
                type="email"
                name="email"
                placeholder="Email address"
                required
              />
              <input
                class="w-full px-4 py-2 border rounded-md dark:bg-darker dark:border-gray-700 focus:outline-none focus:ring focus:ring-primary-100 dark:focus:ring-primary-darker"
                type="password"
                name="password"
                placeholder="Password"
                required
              />

              <div>
                <button
                  type="submit"
                  class="w-full px-4 py-2 font-medium text-center text-white transition-colors duration-200 rounded-md bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker"
                >
                  Login
                </button>
              </div>
            </form>

            <!-- Or -->
            <div class="flex items-center justify-center space-x-2 flex-nowrap">
              <span class="w-20 h-px bg-gray-300"></span>
              <span>OR</span>
              <span class="w-20 h-px bg-gray-300"></span>
            </div>

            <a
              href="#"
              class="flex items-center justify-center px-4 py-2 space-x-2 text-blue-600 transition-all duration-200 rounded-md hover:bg-opacity-80"
            >
              
              <span> Forgot password </span>
            </a>

          </div>
@endsection