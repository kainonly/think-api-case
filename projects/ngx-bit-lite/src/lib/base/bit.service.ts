import {Injectable} from '@angular/core';
import {ConfigService} from './config.service';
import {NavigationExtras, Router} from '@angular/router';

@Injectable()
export class BitService {
  /**
   * Static Path
   */
  static: string;

  /**
   * Upload Path
   */
  uploads: string;

  constructor(private config: ConfigService,
              private router: Router) {
    this.static = config.staticUrl;
    this.uploads = (config.uploadsUrl) ? config.uploadsUrl : config.originUrl + '/' + config.uploadsPath;
  }

  push(commands: any[], extras?: NavigationExtras) {
    this.router.navigate(commands, extras);
  }
}
