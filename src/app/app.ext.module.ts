import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import {IonicModule} from '@ionic/angular';
import {NgZorroAntdMobileModule} from 'ng-zorro-antd-mobile';

@NgModule({
    exports: [
        CommonModule,
        FormsModule,
        ReactiveFormsModule,
        NgZorroAntdMobileModule,
        IonicModule,
    ]
})
export class AppExtModule {
}
