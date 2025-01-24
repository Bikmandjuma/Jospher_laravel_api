@component('mail::message')
    <h2>Assalamualaikum Warahmatullahi Wabarakatuh</h2>
    
    <p>We have a new seeker who has registered on the <b style="color: blue;">{{ config('app.name', 'Job-sphere-rwanda') }}</b> system.</p>

@component('mail::panel')
    <p>The details are as follows:</p>

    <p><b>Firstname:</b> {{ $firstname }}</p><br>
    <p><b>Lastname:</b> {{ $lastname }}</p><br>
    <p><b>Gender:</b> {{ $gender }}</p><br>
    <p><b>Email:</b> {{ $email }}</p><br>
    <p><b>Birthdate:</b> {{ $birthdate }}</p>
@endcomponent

<p>Thank you, and have a great day!</p>
@endcomponent

