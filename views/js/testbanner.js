let bannerButtonClose = document.getElementById('closeBannerButton');
let banner = document.getElementById('testbanner_block_home');

bannerButtonClose.addEventListener("click", () => {
    return banner.style.display = 'none';
})
