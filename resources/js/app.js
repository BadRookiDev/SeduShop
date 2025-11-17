import '@iconify/iconify';
import Iconify from '@iconify/iconify';
import LazyLoad from 'vanilla-lazyload/dist/lazyload.js';

const lazyLoadInstance = new LazyLoad({
    elements_selector: `[data-ll-thresh="sm"]`,
    threshold: 30,
})

//todo split in defer and non defer scripts
