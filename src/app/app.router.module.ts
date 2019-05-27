import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';

const routes: Routes = [
  {path: '', loadChildren: './pages/home/home.module#HomeModule'},
  {path: 'wechat-sdk', loadChildren: './pages/wechat-sdk/wechat-sdk.module#WechatSdkModule'},
  {path: 'scenes', loadChildren: './pages/scenes/scenes.module#ScenesModule'}
];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, {useHash: true})
  ],
  exports: [RouterModule]
})
export class AppRouterModule {
}
