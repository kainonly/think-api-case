import {NgModule} from '@angular/core';
import {BrowserModule} from '@angular/platform-browser';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {HttpClientModule} from '@angular/common/http';
import {NgZorroAntdMobileModule, Toast} from 'ng-zorro-antd-mobile';
import {NgxBitLiteModule} from 'ngx-bit-lite';
import {environment} from '../environments/environment';
import {AppRouterModule} from './app.router.module';

import {AppComponent} from './app.component';
import {TokenService} from './guard/token.service';
import {MainService} from './api/main.service';

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
    AppRouterModule,
  ],
  providers: [
    TokenService,
    MainService,
    Toast
  ],
  bootstrap: [
    AppComponent
  ],
})
export class AppModule {
}
