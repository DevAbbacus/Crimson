const tids = {};
export function delay(callback = () => {}, time = 500, id = undefined) {
    if (!id) id = Date.now();
    return function(...args) {
        clearTimeout(tids[id]);
        tids[id] = setTimeout(() => callback.call(this, ...args), time);
    };
}
