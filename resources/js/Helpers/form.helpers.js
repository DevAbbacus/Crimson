function resolveValue(fn, k, v) {
    if (v instanceof File) return v;
    if (typeof v === 'object') return fn(k, v);
    else return v;
}

function formByJson(json = {}, options = {}) {
    const form = new FormData();
    const rFn = options.objResolver;
    for (const [k, v] of Object.entries(json)) {
        // TODO: Add logic to objects
        if (Array.isArray(v)) {
            for (const item of v || [])
                form.append(`${k}[]`, resolveValue(rFn, k, item));
        } else form.append(k, resolveValue(rFn, k, v));
    }
    return form;
}

module.exports = { formByJson };
