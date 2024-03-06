import {DateTime, Duration} from 'luxon';

interface UseLuxon {
    DateTime: typeof DateTime,
    Duration: typeof Duration,

    timestampToDateTime(timestamp: string | number): DateTime,

    timestampToRelative(timestamp: string | number): string
}

export function useLuxon(): UseLuxon {
    const timestampToDateTime = (timestamp: string | number): DateTime => {
        return DateTime.fromSeconds(Number(timestamp));
    }

    const timestampToRelative = (timestamp: string | number): string => {
        return DateTime.fromSeconds(Number(timestamp)).toRelative();
    }

    return {
        DateTime,
        Duration,
        timestampToDateTime,
        timestampToRelative
    }
}
