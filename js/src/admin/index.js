app.initializers.add('ganuonglachanh-sonic', () => {
  app.extensionData
    .for('ganuonglachanh-sonic')
    .registerSetting(() => <legend>{app.translator.trans('ganuonglachanh-sonic.admin.settings.GNOptionsHeading')}</legend>)
    .registerSetting({
      setting: 'ganuonglachanh-sonic.host',
      label: app.translator.trans('ganuonglachanh-sonic.admin.settings.host'),
      type: 'text',
      placeholder: '127.0.0.1'
    })
    .registerSetting({
      setting: 'ganuonglachanh-sonic.port',
      label: app.translator.trans('ganuonglachanh-sonic.admin.settings.port'),
      type: 'number',
      placeholder: '1491'
    })
    .registerSetting({
      setting: 'ganuonglachanh-sonic.timeout',
      label: app.translator.trans('ganuonglachanh-sonic.admin.settings.timeout'),
      type: 'number',
      placeholder: '30'
    })
    .registerSetting({
      setting: 'ganuonglachanh-sonic.locale',
      label: app.translator.trans('ganuonglachanh-sonic.admin.settings.locale'),
      type: 'text',
      placeholder: 'eng'
    })
});