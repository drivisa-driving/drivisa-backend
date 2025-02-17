export default function status(code) {
    if (code === 1) {
        return "Reserved";
    } else if (code === 2) {
        return "In Progress";
    } else if (code === 3) {
        return "Completed";
    } else if (code === 4) {
        return "Canceled";
    } else {
        return "Unknown";
    }
}