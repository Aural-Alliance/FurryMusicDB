<?php

namespace App\Entity\Enums;

enum SocialTypes: string
{
    case Website = 'website';

    case Bandcamp = 'bandcamp';
    case Beatport = 'beatport';
    case BlueSky = 'bluesky';
    case Mastodon = 'mastodon';
    case Patreon = 'patreon';
    case SoundCloud = 'soundcloud';
    case Twitch = 'twitch';
    case Twitter = 'twitter';
    case Tumblr = 'tumblr';
    case YouTube = 'youtube';
    case Newgrounds = 'newgrounds';

    case Custom = 'custom';
}
