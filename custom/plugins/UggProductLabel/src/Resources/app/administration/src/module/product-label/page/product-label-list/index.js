import template from './product-label-list.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('ugg-product-label-list', {
    template,

    inject: ['repositoryFactory'],

    mixins: [Mixin.getByName('listing')],

    data() {
        return {
            items: null,
            isLoading: false,
            total: 0,
        };
    },

    computed: {
        repository() {
            return this.repositoryFactory.create('product_label');
        },

        columns() {
            return [
                { property: 'name', label: 'Name', routerLink: 'ugg.product.label.detail', primary: true },
                { property: 'color', label: 'Color' },
                { property: 'priority', label: 'Priority', sortable: true },
                { property: 'active', label: 'Active' },
            ];
        },

        listCriteria() {
            const criteria = new Criteria(this.page, this.limit);
            criteria.addSorting(Criteria.sort('priority', 'DESC'));
            return criteria;
        },
    },

    methods: {
        async getList() {
            this.isLoading = true;

            try {
                const result = await this.repository.search(this.listCriteria);
                this.items = result;
                this.total = result.total;
            } finally {
                this.isLoading = false;
            }
        },

        async onDelete(label) {
            await this.repository.delete(label.id);
            await this.getList();
        },
    },
});
