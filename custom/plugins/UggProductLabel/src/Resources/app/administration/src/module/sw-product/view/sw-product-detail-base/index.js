import template from './sw-product-detail-base.html.twig';

Shopware.Component.override('sw-product-detail-base', {
    template,

    inject: [
        'repositoryFactory',
    ],

    watch: {
        product: {
            immediate: true,

            async handler(product) {
                if (!product?.id) {
                    return;
                }

                const repository = this.repositoryFactory.create('product');
                const criteria = new Shopware.Data.Criteria();

                criteria.addAssociation('labels');

                const loadedProduct = await repository.get(
                    product.id,
                    Shopware.Context.api,
                    criteria
                );

                this.product.extensions.labels =
                    loadedProduct.extensions?.labels;
            },
        },
    },
});

