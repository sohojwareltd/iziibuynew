@if ($hasMenu)
    @foreach ($tree as $node)
        @php
            $item = $node['item'];
            $url = $node['url'];
            $children = $node['children'];
        @endphp

        @if ($children->isNotEmpty())
            <li class="{{ $itemClass }} dropdown">
                <a
                    href="{{ $url ?? '#' }}"
                    class="{{ $linkClass }} dropdown-toggle"
                    role="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    @if ($item->open_new_tab && $url) target="_blank" rel="noopener noreferrer" @endif
                >
                    {{ $item->title }}
                </a>
                <ul class="dropdown-menu">
                    @foreach ($children as $childNode)
                        @php
                            $child = $childNode['item'];
                            $childUrl = $childNode['url'];
                        @endphp
                        @if ($childUrl)
                            <li>
                                <a
                                    class="dropdown-item"
                                    href="{{ $childUrl }}"
                                    @if ($child->open_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                >
                                    {{ $child->title }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </li>
        @elseif ($url)
            <li class="{{ $itemClass }}">
                <a
                    href="{{ $url }}"
                    class="{{ $linkClass }}"
                    @if ($item->open_new_tab) target="_blank" rel="noopener noreferrer" @endif
                >
                    {{ $item->title }}
                </a>
            </li>
        @endif
    @endforeach
@elseif ($fallback)
    {{ $slot }}
@endif
