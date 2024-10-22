<li>
    <a href="{{ $item->url or '' }}"
       aria-expanded="false"
       @isset($item->target) target="{{ $item->target or '' }}" @endisset
    >
        @isset($item->class) <i class="{{ $item->class or '' }}"></i> @endisset

        <span>
            {{ $item->title or '' }}
            {!! $item->badge or '' !!}
        </span>
    </a>
</li>