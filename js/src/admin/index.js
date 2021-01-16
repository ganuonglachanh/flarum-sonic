app.initializers.add('ganuonglachanh-sonic', () => {
  app.extensionData
    .for('ganuonglachanh-sonic')
    .registerSetting(() => <legend>{app.translator.trans('ganuonglachanh-sonic.admin.settings.GNOptionsHeading')}</legend>)
    .registerSetting({
      setting: 'ganuonglachanh-sonic.host',
      label: app.translator.trans('ganuonglachanh-sonic.admin.settings.host'),
      type: 'text'
    })
    .registerSetting({
      setting: 'ganuonglachanh-sonic.port',
      label: app.translator.trans('ganuonglachanh-sonic.admin.settings.port'),
      type: 'number'
    })
    .registerSetting({
      setting: 'ganuonglachanh-sonic.timeout',
      label: app.translator.trans('ganuonglachanh-sonic.admin.settings.timeout'),
      type: 'number'
    })
    .registerSetting({
      setting: 'ganuonglachanh-sonic.locale',
      label: app.translator.trans('ganuonglachanh-sonic.admin.settings.locale'),
      type: 'text'
    })
});