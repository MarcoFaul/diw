import template from './sw-cms-preview-{FEATURE_NAME}.html.twig';
import './sw-cms-preview-{FEATURE_NAME}.scss';

const { Component } = Shopware;

Component.register('sw-cms-preview-{FEATURE_NAME}', {
  template
});
