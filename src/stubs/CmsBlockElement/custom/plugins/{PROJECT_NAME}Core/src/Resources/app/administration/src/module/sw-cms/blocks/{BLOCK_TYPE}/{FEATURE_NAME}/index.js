import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
  name: '{FEATURE_NAME}',
  label: 'sw-cms.elements.{FEATURE_NAME}.label',
  category: '{BLOCK_TYPE}',
  component: 'sw-cms-block-{FEATURE_NAME}',
  previewComponent: 'sw-cms-preview-{FEATURE_NAME}',
  defaultConfig: {
    marginBottom: '',
    marginTop: '',
    marginLeft: '',
    marginRight: '',
    sizingMode: 'boxed'
  },
  slots: {
    center: {
      type: '{FEATURE_NAME}',
    }
  }
});
