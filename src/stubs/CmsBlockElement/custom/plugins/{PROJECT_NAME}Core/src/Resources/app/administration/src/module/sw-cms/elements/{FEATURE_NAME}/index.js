import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
  name: '{FEATURE_NAME}',
  label: 'sw-cms.elements.{FEATURE_NAME}.label',
  component: 'sw-cms-el-{FEATURE_NAME}',
  configComponent: 'sw-cms-el-config-{FEATURE_NAME}',
  previewComponent: 'sw-cms-el-preview-{FEATURE_NAME}',
  defaultConfig: {}
});
