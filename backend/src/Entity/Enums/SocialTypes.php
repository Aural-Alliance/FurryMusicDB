<?php

namespace App\Entity\Enums;

enum SocialTypes: string
{
    case Website = 'website';
    case Twitter = 'twitter';
    case Tumblr = 'tumblr';
    case BlueSky = 'bluesky';
    case Mastodon = 'mastodon';
    case SoundCloud = 'soundcloud';
    case Bandcamp = 'bandcamp';
    case Beatport = 'beatport';
    case Custom = 'custom';
}
