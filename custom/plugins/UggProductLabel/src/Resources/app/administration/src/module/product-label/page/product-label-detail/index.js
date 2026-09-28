import template from './product-label-detail.html.twig';

const { Component } = Shopware;

Component.register('ugg-product-label-detail', {
    template,

    inject: [
        'repositoryFactory',
    ],

    data() {
        return {
            label: null,
            repository: null,
            isLoading: false,
            isSaveSuccessful: false,
        };
    },

    computed: {
        isCreateMode() {
            return this.$route.params.id === 'create';
        },
    },

    created() {
        this.repository = this.repositoryFactory.create('product_label');

        this.loadEntity();
    },

    methods: {
        async loadEntity() {
            this.isLoading = true;

            try {
                if (this.isCreateMode) {
                    this.label = this.repository.create(
                        Shopware.Context.api
                    );

                    this.label.products = new Shopware.Data.EntityCollection(
                        '/product',
                        'product',
                        Shopware.Context.api
                    );

                    return;
                }

                const criteria = new Shopware.Data.Criteria();
                criteria.addAssociation('products');

                this.label = await this.repository.get(
                    this.$route.params.id,
                    Shopware.Context.api,
                    criteria
                );

            } finally {
                this.isLoading = false;
            }
        },

        async onSave() {
            this.isLoading = true;
            this.isSaveSuccessful = false;

            try {
                await this.repository.save(
                    this.label,
                    Shopware.Context.api
                );

                this.isSaveSuccessful = true;

                if (this.isCreateMode) {
                    await this.$router.push({
                        name: 'ugg.product.label.detail',
                        params: {
                            id: this.label.id,
                        },
                    });
                }
            } finally {
                this.isLoading = false;
            }
        },
    },
});
