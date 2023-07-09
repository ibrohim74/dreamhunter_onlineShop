/*if ('loading' in HTMLImageElement.prototype && wbcr_robin.wpCompatibleLazy === 'yes') {
    //loading="lazy" in WP >= 5.5
} else {
}*/

const el = document.querySelectorAll('img');
const observer = lozad(el, {
    loaded: function (el) {
    },
});
observer.observe();
