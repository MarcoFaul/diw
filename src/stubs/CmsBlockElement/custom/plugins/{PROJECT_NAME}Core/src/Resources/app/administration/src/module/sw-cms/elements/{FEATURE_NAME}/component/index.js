import template from './sw-cms-el-{FEATURE_NAME}.html.twig';
import './sw-cms-el-{FEATURE_NAME}.scss';

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-{FEATURE_NAME}', {
  template,

  mixins: [
    Mixin.getByName('cms-element')
  ],

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig('{FEATURE_NAME}');
    }
  }
});
