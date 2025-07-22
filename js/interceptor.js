const originalFetch = window.fetch;

window.fetch = function (url, options = {}) {
    const token = localStorage.getItem('auth_token');

    if (url.startsWith('/api/') && token) {
        options.headers = options.headers || {};

        if (options.headers instanceof Headers) {
            options.headers.set('Authorization', token);
            options.headers.set('Accept', 'application/json');
        } else {
            options.headers = {
                ...options.headers,
                Authorization: token,
                Accept: 'application/json'
            };
        }
    }

    return originalFetch(url, options).then(response => {
        if (response.status === 401 && !url.endsWith('/login')) {
            localStorage.clear();
            window.location.href = '/login';
        }
        return response;
    });
};
