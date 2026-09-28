import './page/product-label-list';
import './page/product-label-detail';

Shopware.Module.register('ugg-product-label', {
    type: 'plugin',

    name: 'product-label',

    title: 'Product Labels',

    description: 'Manage product labels',

    color: '#189eff',

    icon: 'regular-tags',

    routes: {
        index: {
            component: 'ugg-product-label-list',
            path: 'index',
        },

        detail: {
            component: 'ugg-product-label-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'ugg.product.label.index',
            },
        },
    },

    navigation: [{
        label: 'Product Labels',
        color: '#189eff',
        path: 'ugg.product.label.index',
        icon: 'regular-tags',
        parent: 'sw-catalogue',
        position: 100,
    }],
});

