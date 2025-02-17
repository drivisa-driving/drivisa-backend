let eventGuid = 0
let todayStr = new Date().toISOString().replace(/T.*$/, '') // YYYY-MM-DD of today

export const INITIAL_EVENTS = [
    {
        id: createEventId(),
        title: '2',
        start: todayStr,
        classNames: "event green"
    },
    {
        id: createEventId(),
        title: '2',
        start: todayStr,
        classNames: "event red"
    },
    {
        id: createEventId(),
        title: '2',
        start: todayStr,
        classNames: "event light-blue"
    },
    {
        id: createEventId(),
        title: '2',
        start: todayStr,
        classNames: "event blue"
    },
]

export function createEventId() {
    return String(eventGuid++)
}
