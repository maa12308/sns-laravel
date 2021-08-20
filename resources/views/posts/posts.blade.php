@if (count($posts) > 0)
    <ul class="list-unstyled">
        @foreach ($posts as $post)
            <li class="media mb-3">
                {{-- 投稿の所有者のメールアドレスをもとにGravatarを取得して表示 --}}
                <img class="mr-2 rounded" src="{{ Gravatar::get($post->user->email, ['size' => 50]) }}" alt="">
                <div class="media-body">
                    <div>
                        {{-- 投稿の所有者のユーザ詳細ページへのリンク --}}
                        {!! link_to_route('users.show', $post->user->name, ['user' => $post->user->id]) !!}
                        <span class="text-muted">posted at {{ $post->created_at }}</span>
                    </div>
                    <div>
                         {{-- 投稿内容 --}}
                        <p class="mb-0">{!! nl2br(e($post->content)) !!}</p>
                    </div>
                <div>
                    <a href="{{ Storage::disk('s3')->url($post->image) }}" data-lightbox="group"><img class="image" src="{{ Storage::disk('s3')->url($post->image) }}"></a>
                </div>
                    <div class="d-flex justify-content-start">
                        <div class="mr-2">
                        {{-- お気に入り追加／削除ボタン --}}
                        @include('posts_favorite.favorite_button')
                        </div>
                        @if (Auth::id() == $post->user_id)
                            <div>
                            {{-- 投稿削除ボタンのフォーム --}}
                            {!! Form::open(['route' => ['posts.destroy', $post->id], 'method' => 'delete']) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm']) !!}
                            {!! Form::close() !!}
                            </div>
                        @endif
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
    {{-- ページネーションのリンク --}}
    {{ $posts->links() }}
@endif