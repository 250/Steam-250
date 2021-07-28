export function parseParam(name: string) {
    const match = RegExp('[?&]' + name + '=([^&]*)').exec(location.search);

    return match && decodeURIComponent(match[1]);
}
