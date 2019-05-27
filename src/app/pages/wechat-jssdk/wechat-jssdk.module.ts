import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {WechatJssdkComponent} from './wechat-jssdk.component';
import {AppExtModule} from '../../app.ext.module';

const routes: Routes = [
  {
    path: '',
    component: WechatJssdkComponent
  }
];

@NgModule({
  imports: [
    AppExtModule,
    RouterModule.forChild(routes)
  ],
  declarations: [WechatJssdkComponent]
})
export class WechatJssdkModule {
}
