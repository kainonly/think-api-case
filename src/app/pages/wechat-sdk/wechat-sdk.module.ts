import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {WechatSdkComponent} from './wechat-sdk.component';
import {AppExtModule} from '../../app.ext.module';

const routes: Routes = [
  {
    path: '',
    component: WechatSdkComponent
  }
];

@NgModule({
  imports: [
    AppExtModule,
    RouterModule.forChild(routes)
  ],
  declarations: [WechatSdkComponent]
})
export class WechatSdkModule {
}
