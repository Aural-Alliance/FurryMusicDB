import {DateTime, Duration} from 'luxon';

interface UseLuxon {
    DateTime: typeof DateTime,
    Duration: typeof Duration,

    isoToRelative(string): string,

    isoToTimestamp(string): number
}

export function useLuxon(): UseLuxon {
    const isoToRelative = (datetime: string): string => {
        return DateTime.fromISO(datetime).toRelative();
    }

    const isoToTimestamp = (datetime: string): number => {
        return DateTime.fromISO(datetime).toUnixInteger();
    }

    return {
        DateTime,
        Duration,
        isoToRelative,
        isoToTimestamp,
    }
}
