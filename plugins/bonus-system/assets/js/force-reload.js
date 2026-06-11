(function () {
    function isCartQuery(url) {
        return url && (url.includes("wc/store") || url.includes("cart") || url.includes("wc/"));
    }

    let isReloading = false;
    function reloadPage() {
        if (!isReloading) {
            isReloading = true;
            window.location.reload();
        }
    }
    const originalFetch = window.fetch;
    window.fetch = function () {
        const url = arguments[0];
        const promise = originalFetch.apply(this, arguments);
        if (typeof url === "string" && isCartQuery(url)) {
            promise.then(function (response) {
                if (response.ok && !isReloading) {
                    reloadPage();
                }
            }).catch(function () { });
        }
        return promise;
    };
    const originalOpen = XMLHttpRequest.prototype.open;
    const originalSend = XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.open = function () {
        this._url = arguments[1];
        return originalOpen.apply(this, arguments);
    };
    XMLHttpRequest.prototype.send = function () {
        const url = this._url;
        this.addEventListener("load", function () {
            if (this.status === 200 && isCartQuery(url)) {
                if (!isReloading) {
                    reloadPage();
                }
            }
        });
        return originalSend.apply(this, arguments);
    };
})();