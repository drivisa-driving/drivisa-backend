export default function dateTime(date, showFullDateTime = true) {
    date = Date.parse(date)

    let newDate = new Date(date)

    var hours = newDate.getHours();
    var minutes = newDate.getMinutes();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    let final = hours + ':' + minutes + ' ' + ampm;
    if (showFullDateTime) {
        final = newDate.toLocaleDateString("en-US", {
            "month": "short",
            "day": "numeric",
            "year": "numeric",
        }) + " " + final;
    }

    return final;
}
