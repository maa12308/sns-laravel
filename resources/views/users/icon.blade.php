

<div class="icon text-center">
            <img class="rounded circle" src="{{ Gravatar::get($user->email, ['size' => 150]) }}" alt="">
        </div>
        <div class="name text-center">
            <h1>{{ $user->name }}</h1>
        </div>
        
{{-- フォロー／アンフォローボタン --}}
@include('user_follow.follow_button')