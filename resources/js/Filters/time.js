export default function time(date) {
    let time = date.split(":");
    let newDate = new Date();

    newDate.setHours(time[0]);
    newDate.setMinutes(time[1]);
    newDate.setSeconds(time[2]);

    var hours = newDate.getHours();
    var minutes = newDate.getMinutes();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    return hours + ':' + minutes + ' ' + ampm;
}
