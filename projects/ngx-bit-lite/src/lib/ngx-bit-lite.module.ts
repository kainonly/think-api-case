import {ModuleWithProviders, NgModule} from '@angular/core';
import {HttpClientModule} from '@angular/common/http';
import {ConfigService} from './base/config.service';
import {BitService} from './base/bit.service';
import {EventsService} from './base/events.service';
import {HttpService} from './base/http.service';
import {CommonService} from './base/common.service';
import {WechatService} from './plugin/wechat.service';

@NgModule({
  imports: [HttpClientModule]
})
export class NgxBitLiteModule {
  static forRoot(config: any): ModuleWithProviders<NgxBitLiteModule> {
    return {
      ngModule: NgxBitLiteModule,
      providers: [
        BitService,
        HttpService,
        EventsService,
        CommonService,
        WechatService,
        {provide: ConfigService, useValue: config},
      ],
    };
  }
}
