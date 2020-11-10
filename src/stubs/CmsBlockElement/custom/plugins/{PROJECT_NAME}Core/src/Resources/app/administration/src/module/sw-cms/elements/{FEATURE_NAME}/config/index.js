import template from './sw-cms-el-config-{FEATURE_NAME}.html.twig';

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-config-{FEATURE_NAME}', {
  template,
  mixins: [
    Mixin.getByName('cms-element')
  ],

  inject: ['repositoryFactory'],

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig('{FEATURE_NAME}');
    },
  }
});
