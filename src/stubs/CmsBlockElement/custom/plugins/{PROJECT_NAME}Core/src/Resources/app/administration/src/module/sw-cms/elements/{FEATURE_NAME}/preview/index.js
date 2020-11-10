import template from './sw-cms-el-preview-{FEATURE_NAME}.html.twig';
import './sw-cms-el-preview-{FEATURE_NAME}.scss';

const { Component } = Shopware;

Component.register('sw-cms-el-preview-{FEATURE_NAME}', {
  template
});
