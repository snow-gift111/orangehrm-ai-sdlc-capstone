package com.orangehrm.lba.security;

import com.orangehrm.lba.config.LbaProperties;
import org.springframework.stereotype.Component;

@Component("lbaSecurity")
public class SecurityExpressions {

    private final LbaProperties props;

    public SecurityExpressions(LbaProperties props) {
        this.props = props;
    }

    public String ruleManageAuthority() {
        return props.security().ruleManageAuthority();
    }

    public String historyViewAuthority() {
        return props.security().historyViewAuthority();
    }
}
