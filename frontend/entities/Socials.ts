export interface Social {
    id: string,
    type: SocialType,
    name: string | null,
    value: string
}

export enum SocialType {
    Website = 'website',

    Bandcamp = 'bandcamp',
    Beatport = 'beatport',
    BlueSky = 'bluesky',
    Mastodon = 'mastodon',
    Patreon = 'patreon',
    SoundCloud = 'soundcloud',
    Twitch = 'twitch',
    Twitter = 'twitter',
    Tumblr = 'tumblr',
    Newgrounds = 'newgrounds',
    YouTube = 'youtube',

    Custom = 'custom'
}

export function getSocialName(type: SocialType, customLabel?: string): string {
    switch (type) {
        case SocialType.Bandcamp:
            return 'Bandcamp';

        case SocialType.Beatport:
            return 'Beatport';

        case SocialType.BlueSky:
            return 'BlueSky';

        case SocialType.Mastodon:
            return 'Mastodon';

        case SocialType.Patreon:
            return 'Patreon';

        case SocialType.SoundCloud:
            return 'SoundCloud';

        case SocialType.Twitch:
            return 'Twitch';

        case SocialType.Twitter:
            return 'X (Twitter)';

        case SocialType.Tumblr:
            return 'Tumblr';

        case SocialType.Newgrounds:
            return 'Newgrounds';

        case SocialType.YouTube:
            return 'YouTube';

        case SocialType.Website:
            return 'Web Site';

        case SocialType.Custom:
        default:
            return customLabel ?? 'Custom';
    }
}
