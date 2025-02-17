export default function date(date) {
    date = new Date(date);

    return date.toLocaleDateString("en-US", {
        "month": "short",
        "day": "numeric",
        "year": "numeric",
    })
}