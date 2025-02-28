@extends('admin.user.cover')
@section('content')

  <div class="flex-col min-h-screen">
    <div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-primary-darker">
      <h1 class="text-2xl font-semibold">Paid users</h1>
        <a
                href="{{ route('owner.display_paid_users') }}"
                class="px-4 py-2 text-sm text-white rounded-md bg-primary hover:bg-primary-dark focus:outline-none focus:ring focus:ring-primary focus:ring-offset-1 focus:ring-offset-white dark:focus:ring-offset-dark"
              >
                View paid users
        </a>
    </div>

    <div class="container mx-auto p-4">
        
      <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
          <div>
            <h2 class="text-2xl font-semibold leading-tight">Assign payment to a user</h2>
          </div>
          <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div
              class="inline-block min-w-full shadow-md rounded-lg overflow-hidden"
            >

        <!-- Users Table -->
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md leading-normal">
            <thead>
                <tr>
                  <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                  >
                    User_names / Code
                  </th>
                  <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                  >
                    Email/Phone
                  </th>
                  <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                  >
                     DoB/ Gender
                  </th>
                 
                  <th
                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"
                  ></th>
                </tr>
              </thead>
            <tbody>
                @forelse($users as $user)
                
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                      <div class="flex">
                        <div class="flex-shrink-0 w-10 h-10">
                          <img
                            class="w-full h-full rounded-full"
                            src="{{ URL::to('/') }}/users/images/user.jpg"
                            alt=""
                          />
                        </div>
                        <div class="ml-3">
                          <p class="text-gray-900 whitespace-no-wrap">
                            {{ $user->firstname }} {{ $user->lastname }}
                          </p>
                          <p class="text-gray-600 whitespace-no-wrap">{{ $user->user_code }}</p>
                        </div>
                      </div>
                    </td>
                    
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                      <p class="text-gray-900 whitespace-no-wrap">{{ $user->email }}</p>
                      <p class="text-gray-600 whitespace-no-wrap">{{ $user->phone }}</p>
                    </td>

                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                      <p class="text-gray-900 whitespace-no-wrap">{{ $user->birthdate }}</p>
                      <p class="text-gray-600 whitespace-no-wrap">{{ $user->gender }}</p>
                    </td>
                 
              </tr>
              

                @empty
                <tr>
                    <td colspan="4" class="p-3 text-center text-gray-700">User not found</td>
                </tr>
                @endforelse

            </tbody>

        </table>
        
      </div>

      <div class="container mx-auto p-4 flex flex-wrap">
          <div class="w-full lg:w-1/3 md:w-1/2"></div>
          <div class="w-full lg:w-1/3 md:w-1/2 bg-white">
            <h3>Assign payment</h3>
            <form action="{{ route('owner.submit_payment_ToUser',$user_id) }}" method="POST">
              @csrf
              <input
                class="w-full px-4 py-2 border rounded-md dark:bg-darker dark:border-gray-700 focus:outline-none focus:ring focus:ring-primary-100 dark:focus:ring-primary-darker"
                type="text"
                name="amount"
                placeholder="Enter amount"
                required
              />
              <input
                class="w-full px-4 py-2 p-2 border rounded-md dark:bg-darker dark:border-gray-700 focus:outline-none focus:ring focus:ring-primary-100 dark:focus:ring-primary-darker"
                type="text"
                name="duration"
                placeholder="Enter duration"
                required
              />
<!--               <input type="text" name="amount" placeholder="Enter amount">
              <input type="text" name="amount" placeholder="Enter duration"> -->
              <button type="submit" class="w-full px-4 py-2 font-medium text-center text-white transition-colors duration-200 rounded-md bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker">Pay</button>
            </form>
            </div>
          <div class="w-full lg:w-1/3 md:w-1/2"></div>
        
      </div>

      </div>
    </div>
  </div>

        
    </div>

           
             
  </div>

@endsection