import {Component} from '@angular/core';
import {BitService, CommonService} from 'ngx-bit-lite';
import {environment} from '../../../environments/environment';

@Component({
  selector: 'app-scenes',
  templateUrl: './scenes.component.html',
  styleUrls: ['./scenes.component.scss']
})
export class ScenesComponent {
  constructor(private common: CommonService,
              public bit: BitService) {
  }

  startOauth() {
    this.common.redirect(environment.wechatOauthUrl);
  }
}
