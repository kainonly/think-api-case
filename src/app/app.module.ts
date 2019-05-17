import {NgModule} from '@angular/core';
import {BrowserModule} from '@angular/platform-browser';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {HttpClientModule} from '@angular/common/http';
import {RouterModule, Routes} from '@angular/router';
import {NgZorroAntdMobileModule} from 'ng-zorro-antd-mobile';

import {AppComponent} from './app.component';
import {TokenService} from './guard/token.service';
import {MainService} from './api/main.service';

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
