module.exports = {
    createLinks(uri, pagination) {
        if (!pagination) return [];
        const { page, per_page, total_row_count } = pagination;
        const maxpages = Math.ceil(total_row_count / per_page);
        const prev = {
            label: 'Previous',
            url: page <= 1 ? null : `${uri}?page=${page - 1}`
        };
        const next = {
            label: 'Next',
            url: page >= maxpages ? null : `${uri}?page=${page + 1}`
        };
        const pageLinks = [{ label: page, url: null, active: true }];
        for (let i = 1; i < 5; i++) {
            const prevPage = page - i;
            const nextPage = page + i;
            if (prevPage >= 1)
                pageLinks.unshift({
                    label: prevPage,
                    url: `${uri}?page=${prevPage}`
                });
            if (nextPage <= maxpages)
                pageLinks.push({
                    label: nextPage,
                    url: `${uri}?page=${nextPage}`
                });
        }
        return [prev, ...pageLinks, next];
    }
};
