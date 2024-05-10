import React from 'react';
import { StyleSheet, FlatList, View } from 'react-native';
import {
  NavBar, Container, IconButton,
} from 'components';
import { scale, verticalScale } from 'react-native-size-matters';
import { getPosts } from 'mocks/posts';
import PropTypes from 'prop-types';
import PostCard from './PostCard';
import PromoCard from './PromoCard';

const styles = StyleSheet.create({
  container: {
    paddingHorizontal: scale(14),
  },
  header: {
    marginTop: verticalScale(10),
  },
  footer: {
    marginBottom: verticalScale(10),
  },
  rightIcons: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  icon: {
    marginLeft: scale(20),
  },
});

const Feed = ({ navigation }) => (
  <Container>
    <NavBar
      title="Feed"
      renderRightComponent={() => (
        <View style={styles.rightIcons}>
          <IconButton
            icon="search"
            color="white"
            size={22}
            onPress={() => navigation.navigate('Search')}
          />
          <IconButton
            icon="user-plus"
            color="white"
            style={styles.icon}
            size={22}
            onPress={() => navigation.navigate('Social')}
          />
        </View>
      )}
    />

    <FlatList
      style={styles.container}
      data={getPosts()}
      keyExtractor={(data) => data.id.toString()}
      renderItem={({ item }) => (
        <PostCard {...item} navigation={navigation} />
      )}
      // ListHeaderComponent={(
      //   <View style={styles.header}>
      //     <PromoCard />
      //   </View>
      // )}
      ListFooterComponent={<View style={styles.footer} />}
    />
  </Container>
);

Feed.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default Feed;
