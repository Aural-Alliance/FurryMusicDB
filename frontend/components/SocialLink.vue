<template>
    <dt class="col-lg-2 col-md-3 col-sm-6 text-end">
        <icon :icon="rowIcon"/>
        {{ rowLabel }}
    </dt>
    <dd class="col-lg-10 col-md-9 col-sm-6 text-start">
        <a v-if="rowLink" :href="rowLink" target="_blank">
            {{ row.value }}
        </a>
        <span v-else>
            {{ row.value }}
        </span>
    </dd>
</template>

<script setup lang="ts">
import Icon from "~/components/Common/Icon.vue";
import {getSocialName, Social, SocialType} from "~/entities/Socials.ts";
import {computed} from "vue";
import SocialLinks from "social-links";

const props = defineProps<{
    row: Social,
}>();

const rowIcon = computed(() => {
    switch (props.row.type) {
        case SocialType.Mastodon:
            return 'mastodon';

        case SocialType.Twitch:
            return 'twitch';

        case SocialType.Twitter:
            return 'twitter-x';

        case SocialType.YouTube:
            return 'youtube';

        case SocialType.SoundCloud:
        case SocialType.Bandcamp:
        case SocialType.Beatport:
            return 'music-note-list';

        case SocialType.Patreon:
        case SocialType.Tumblr:
        case SocialType.BlueSky:
        case SocialType.Website:
        case SocialType.Custom:
        default:
            return 'globe';
    }
});

const rowLabel = computed(() => {
    return getSocialName(props.row.type, props.row.name);
});

const socialLinks = new SocialLinks();

const rowLink = computed(() => {
    switch (props.row.type) {
        case SocialType.Patreon:
        case SocialType.Twitch:
        case SocialType.Tumblr:
        case SocialType.Twitter:
        case SocialType.YouTube:
            return socialLinks.sanitize(
                props.row.type,
                props.row.value
            );

        default:
            return props.row.value;
    }
});
</script>
