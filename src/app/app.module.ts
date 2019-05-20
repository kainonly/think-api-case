import {NgModule} from '@angular/core';
import {BrowserModule} from '@angular/platform-browser';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {HttpClientModule} from '@angular/common/http';
import {RouterModule, Routes} from '@angular/router';
import {NgZorroAntdMobileModule} from 'ng-zorro-antd-mobile';

import {AppComponent} from './app.component';
import {TokenService} from './guard/token.service';
import {MainService} from './api/main.service';
import {NgxBitLiteModule} from 'ngx-bit-lite';
import {environment} from '../environments/environment';

const routes: Routes = [
  {path: '', loadChildren: './pages/home/home.module#HomeModule'},
];

@NgModule({
  declarations: [
    AppComponent
  ],
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    HttpClientModule,
    NgZorroAntdMobileModule,
    NgxBitLiteModule.forRoot(environment.bit),
    RouterModule.forRoot(routes, {useHash: true}),
  ],
  providers: [
    TokenService,
    MainService
  ],
  bootstrap: [
    AppComponent
  ]
})
export class AppModule {
}
